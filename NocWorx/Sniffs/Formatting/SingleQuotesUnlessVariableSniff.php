<?php
namespace NocWorx\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class SingleQuotesUnlessVariableSniff implements Sniff
{


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return [T_CONSTANT_ENCAPSED_STRING];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being
     *  checked.
     * @param int $stackPtr The position of the current token in the
     *  stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['type'] === 'T_CONSTANT_ENCAPSED_STRING') {
            
            // if it starts with a double quote
            if ($tokens[$stackPtr]['content']{0} ==='"') {
                // ...and contains a single quote, we're good.
                if (strpos($tokens[$stackPtr]['content'] , "'")) {
                    return;
                }

                // ...and contains a carrage return
                if (strpos($tokens[$stackPtr]['content'] , '\n')) {
                    return;
                }
            
                // Otherwise, it's an error.
                $error = 'Only use Double Quotes when a variable is being ' .
                    'inserted.';
                $data = [trim($tokens[$stackPtr]['content'])];
                
                $phpcsFile->addError(
                    $error,
                    $stackPtr,
                    'FOUND',
                    $data
                );
            }
        }
    }

}