<?php

/**
 *
 * This file is part of the Xhtml formatter
 *
 * Copyright (c) 2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace XhtmlFormatter;


class Formatter
{

	const
		SPACES_INDENTATION = 'spacesIndentation',
		TABS_INDENTATION = 'tabsIndentation',

		CONTENT_HTML = 'contentHtml',
		CONTENT_XHTML = 'contentXhtml',
		CONTENT_XML = 'contentXml',

		TOKEN_OPEN_TAG = 'openTag',
		TOKEN_UNPAIRED_TAG = 'unpairedTag',
		TOKEN_CLOSE_TAG = 'closeTag',
		TOKEN_TEXT = 'text',

		OPEN_TAG_RE = '/<(?<element>[-\w]+)(?:[^>]+)?>/',
		CLOSE_TAG_RE = '/^<\/[^\>]+>/',
		PREG_SPLIT_RE = '/(<\/?[-\w]+(?:>|.*?[^?]>))/',

		CODE_PLACEHOLDER_NAMESPACE_PREFIX = 'codePlaceholder_',
		CODE_PLACEHOLDER_RE = '/' . self::CODE_PLACEHOLDER_NAMESPACE_PREFIX . '\d+/',
		CODE_PLACEHOLDERS_REGULAR_EXPRESSIONS = [
			'/<\?php (?:.|\n)*\?>/Um', // php code
			'/[\w\:-]+=(?:"[^"]*"|\'[^\']*\'|\S+)/', // element attribute
			'/<(?:formatter-off|script|style)([-\w]+)?(?:[^>]+)?>(?:.|\n)*<\/(?:formatter-off|script|style)>/Um', // skipped elements
		];

	/**
	 * @var array
	 */
	private $codePlaceholders = [];

	/**
	 * @var string
	 */
	private $contentType = self::CONTENT_HTML;

	/**
	 * @var string
	 */
	private $output;

	/**
	 * @var string
	 */
	private $outputIndentation;

	/**
	 * @var string
	 */
	private $outputIndentationMethod = self::TABS_INDENTATION;

	/**
	 * @var int
	 */
	private $outputIndentationSize;

	/**
	 * @var string
	 */
	private $outputIndentationUnit;

	/**
	 * @var string
	 */
	private $previousTokenType;

	/**
	 * @var array
	 */
	private $unpairedElements = [
		self::CONTENT_HTML => [
			'area', 'base', 'br', 'code', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source',
			'track', 'wbr'
		],
		self::CONTENT_XML => [
			'cloud'
		]
	];

	/**
	 * @var bool
	 */
	private $xmlSyntax = FALSE;


	public function addUnpairedElements(string $elements, string $contentType = NULL): self
	{
		$contentType = $contentType ?? self::CONTENT_HTML;
		$elements = explode(' ', trim($elements));

		foreach ($elements as $element) {
			$this->unpairedElements[$contentType][] = $element;
		}

		return $this;
	}


	public function format(string $string): string
	{
		$this->output = '';
		$this->outputIndentation = '';
		$this->outputIndentationUnit = $this->outputIndentationMethod === self::TABS_INDENTATION
			? "\t"
			: str_repeat(' ', $this->outputIndentationSize);

		$string = $this->setPlaceholders($string);

		$tokens = preg_split(
			self::PREG_SPLIT_RE, $string, -1,
			PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
		);

		foreach ($tokens as $token) {
			$this->formatToken($token);
		}

		$this->removeBlankLines();
		$this->unsetPlaceholders();
		$this->addBlankLine();

		return $this->output;
	}


	public function setContentType(string $contentType): self
	{
		$this->xmlSyntax = in_array($contentType, [self::CONTENT_XHTML, self::CONTENT_XML], TRUE);

		if ($contentType === self::CONTENT_XHTML) {
			$contentType = self::CONTENT_HTML;
		}

		$this->contentType = $contentType;

		return $this;
	}


	public function setSpacesIndentationMethod(int $indentationSize): self
	{
		$this->outputIndentationMethod = self::SPACES_INDENTATION;
		$this->outputIndentationSize = $indentationSize;

		return $this;
	}


	private function addBlankLine()
	{
		if ( ! preg_match("/\n$/", $this->output)) {
			$this->output .= "\n";
		}
	}


	private function decreaseIndentation()
	{
		$this->outputIndentation = preg_replace(
				"/" . $this->outputIndentationUnit . "/",
				'',
				$this->outputIndentation,
				1
			) ?? '';
	}


	private function formatToken(string $token)
	{
		$token = trim($token);

		if ( ! $token) {
			return;
		}

		$type = $this->matchTokenType($token);
		$previousTokenIsOpenTag = $this->previousTokenType === self::TOKEN_OPEN_TAG;
		$previousTokenIsText = $this->previousTokenType === self::TOKEN_TEXT;
		$tokenIsOpenTag = $type === self::TOKEN_OPEN_TAG;
		$tokenIsUnpairedTag = $type === self::TOKEN_UNPAIRED_TAG;
		$tokenIsCloseTag = $type === self::TOKEN_CLOSE_TAG;
		$tokenIsText = $type === self::TOKEN_TEXT;
		$emptyElement = $tokenIsCloseTag && $previousTokenIsOpenTag;
		$connectedText = $tokenIsText && $previousTokenIsText;

		if ($previousTokenIsOpenTag && ($tokenIsOpenTag || $tokenIsUnpairedTag || $tokenIsText)) {
			$this->increaseIndentation();

		} elseif ($tokenIsCloseTag && ! $previousTokenIsOpenTag) {
			$this->decreaseIndentation();
		}

		if ( ! ( ! $this->output && ! $this->previousTokenType || $connectedText || $emptyElement)) {
			$this->output .= "\n";
		}

		if ( ! ( ! $token || $emptyElement || $connectedText)) {
			$this->output .= $this->outputIndentation;
		}

		$this->output .= $token;
		$this->previousTokenType = $type;
	}


	private function increaseIndentation()
	{
		$this->outputIndentation .= $this->outputIndentationUnit;
	}


	private function matchCloseTag(string $string): bool
	{
		return (bool) preg_match(self::CLOSE_TAG_RE, $string);
	}


	private function matchOpenTag(string $string, array &$matches = NULL): bool
	{
		return (bool) preg_match(self::OPEN_TAG_RE, $string, $matches);
	}


	private function matchTokenType(string $token): string
	{
		$type = self::TOKEN_TEXT;

		if ($this->matchOpenTag($token, $matches)) {
			$type = in_array($matches['element'], $this->unpairedElements[$this->contentType])
				? self::TOKEN_UNPAIRED_TAG
				: self::TOKEN_OPEN_TAG;

		} elseif ($this->matchCloseTag($token)) {
			$type = self::TOKEN_CLOSE_TAG;
		}

		return $type;
	}


	private function removeBlankLines()
	{
		$this->output = str_replace("\n\n", "\n", $this->output);
	}


	private function setPlaceholders(string $string): string
	{
		foreach (self::CODE_PLACEHOLDERS_REGULAR_EXPRESSIONS as $codePlaceholderRe) {
			preg_match_all($codePlaceholderRe, $string, $matches);

			foreach ($matches[0] as $key => $code) {
				$placeholderId = uniqid();
				$string = preg_replace(
					'/' . preg_quote($code, '/') . '/',
					self::CODE_PLACEHOLDER_NAMESPACE_PREFIX . $placeholderId, $string,
					1
				);

				$this->codePlaceholders[$placeholderId] = $code;
			}
		}

		return $string;
	}


	private function unsetPlaceholders()
	{
		foreach (array_reverse($this->codePlaceholders) as $codePlaceholderId => $code) {
			$this->output = str_replace(self::CODE_PLACEHOLDER_NAMESPACE_PREFIX . $codePlaceholderId, $code, $this->output);
		}

		$this->codePlaceholders = [];
	}

}
