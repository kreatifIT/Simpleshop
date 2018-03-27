<?php
/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


use CodiceFiscale\Checker;
use CodiceFiscale\Simple;
use CodiceFiscale\Subject;

class FiscalcodeCheck
{

    public static function validateField($value, $params = [])
    {
        // first check general correctness
        $Simple = new Simple();
        $Simple->initValidation();
        $Simple->SetCF($value);

        if (!$Simple->GetCodiceValido()) {
            throw new \ErrorException('fiscal_code_not_valid');
        }
        else if (isset($params['perfectMatch']) && $params['perfectMatch']) {
            $comune = $Simple->GetComuneNascita();

            // check name and birthdate to be sure the code is valid
            $Subject = new Subject([
                'gender'       => $params['gender'],
                'name'         => $params['firstname'],
                'surname'      => $params['lastname'],
                'birthDate'    => $params['birthdate'],
                'belfioreCode' => $comune,
            ]);
            $Checker = new Checker($Subject, [
                'codiceFiscaleToCheck' => strtoupper($value),
                'omocodiaLevel'        => Checker::ALL_OMOCODIA_LEVELS,
            ]);

            if (!$Checker->check()) {
                throw new \ErrorException('fiscal_code_not_valid');
            }
            else if (array_key_exists('birthlocation', $params) && $params['birthlocation'] != $comune) {
                throw new \ErrorException('fiscal_code_not_valid');
            }
        }
    }
}