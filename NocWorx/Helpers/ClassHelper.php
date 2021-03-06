<?php
declare(strict_types = 1);

/**
 * @copyright Copyright (c) 2015 Slevomat.cz, s.r.o.
 * @license   https://github.com/slevomat/coding-standard/blob/master/LICENSE.md MIT Licence
 */

namespace NocWorx\Helpers;

class ClassHelper
{

	public static function getFullyQualifiedName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $classPointer): string
	{
		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, self::getName($codeSnifferFile, $classPointer));
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	public static function getName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $classPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[TokenHelper::findNext($codeSnifferFile, T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllNames(\PHP_CodeSniffer\Files\File $codeSnifferFile): array
	{
		$previousClassPointer = 0;

		return array_map(
			function (int $classPointer) use ($codeSnifferFile): string {
				return self::getName($codeSnifferFile, $classPointer);
			},
			iterator_to_array(self::getAllClassPointers($codeSnifferFile, $previousClassPointer))
		);
	}

	private static function getAllClassPointers(\PHP_CodeSniffer\Files\File $codeSnifferFile, int &$previousClassPointer): \Generator
	{
		do {
			$nextClassPointer = TokenHelper::findNext($codeSnifferFile, TokenHelper::$typeKeywordTokenCodes, $previousClassPointer + 1);
			if ($nextClassPointer === null) {
				break;
			}

			$previousClassPointer = $nextClassPointer;
			yield $nextClassPointer;
		} while (true);
	}

}
