<?php

declare(strict_types=1);

namespace App\Notify;

use App\Entity\Version;

final class MessageFormatter
{
    public function format(Version $version): string
    {
        $message = '<b>' . $version->getModule()->getTitle() . '</b>';
        $message .= ' (' . $version->getModule()->getCode() . ')';

        $message .= "\n\n";

        $message .= '<b>' . $version->getNumber() . '</b>';
        $message .= ' <i>' . $version->getDate()->format('Y-m-d') . '</i>';

        $message .= "\n\n";

        $message .= trim($version->getDescription());

        $message = str_replace('<li>', "\n· ", $message);

        $message = strip_tags($message, ['b', 'i', 'u', 's', 'a', 'code', 'pre']);

        // remove redundant new lines
        $message = preg_replace('/[\r\n]{2,}/', "\n\n", $message);

        // remove redundant spaces
        $message = preg_replace('/[ \t]{2,}/', '  ', $message);

        if (mb_strlen($message) > 4000) {
            $moduleCode = $version->getModule()->getCode();
            $moduleUrl = 'https://dev.1c-bitrix.ru/docs/versions.php?lang=ru&module=' . $moduleCode;
            $message = mb_substr($message, 0, 4000);
            $message .= "\n\n<a href=\"$moduleUrl\">Читать полностью</a>";
        }

        return $message;
    }
}
