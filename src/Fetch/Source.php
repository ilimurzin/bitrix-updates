<?php

declare(strict_types=1);

namespace App\Fetch;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Source
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /**
     * @return ModuleVersions[]
     */
    public function fetchModulesVersions(): array
    {
        $response = $this->client->request(
            'GET',
            'https://dev.1c-bitrix.ru/docs/versions.php',
            [
                'timeout' => 5,
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $content = $response->getContent();

        preg_match(
            '/bxajaxid=(.*?)\'/',
            $content,
            $matches
        );

        $ajaxId = $matches[1] ?? throw new \RuntimeException();

        preg_match_all(
            '/<a (.*?)>(?<title>.*?)<\/a> \((?<code>[a-z]*?)\)/',
            $content,
            $matches,
            PREG_SET_ORDER,
        );

        $modulesVersions = [];

        foreach ($matches as $module) {
            $modulesVersions[] = $this->fetchModuleVersions($ajaxId, $module['code']);
        }

        return $modulesVersions;
    }

    private function fetchModuleVersions(string $ajaxId, string $moduleCode): ModuleVersions
    {
        $response = $this->client->request(
            'GET',
            'https://dev.1c-bitrix.ru/docs/versions.php',
            [
                'query' => [
                    'lang' => 'ru',
                    'module' => $moduleCode,
                    'bxajaxid' => $ajaxId,
                ],
                'timeout' => 5,
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $content = $response->getContent();

        preg_match(
            '/<b>(.*?)<\/b>/',
            $content,
            $matches
        );

        $moduleTitle = $matches[1] ?? '';

        preg_match_all(
            '/<b>v(?<number>.*?)<\/b>.*?<i>(?<date>.*?)<\/i><br>(?<description>.*?)(?=<b>v)/mius',
            $content,
            $matches,
            PREG_SET_ORDER,
        );

        $versions = [];

        foreach ($matches as $version) {
            $versions[] = new Version(
                $version['number'],
                substr($version['date'], 0, 10),
                $version['description'],
            );
        }

        return new ModuleVersions(
            $moduleCode,
            $moduleTitle,
            $versions
        );
    }
}
