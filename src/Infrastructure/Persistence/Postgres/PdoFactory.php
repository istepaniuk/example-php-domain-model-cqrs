<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence\Postgres;

use PDO;

final class PdoFactory
{
    private string $url;
    private PDO $pdo;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getConnection(): PDO
    {
        if (null === $this->pdo) {
            $this->pdo = new PDO(
                $this->buildDsnFromUrl($this->url),
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        }

        return $this->pdo;
    }

    private function buildDsnFromUrl(string $url): string
    {
        $params = parse_url($url);

        if (false === $params) {
            throw new \InvalidArgumentException('Malformed URL.');
        }
        if (!isset($params['scheme'])) {
            throw new \InvalidArgumentException('Missing URL scheme.');
        }
        if ('postgres' !== $params['scheme'] && 'postgresql' !== $params['scheme']) {
            throw new \InvalidArgumentException(sprintf('Unsupported URL scheme "%s".', $params['scheme']));
        }

        return sprintf(
            'pgsql:host=%s;port=%s;user=%s;password=%s;dbname=%s',
            $params['host'],
            $params['port'],
            $params['user'],
            $params['pass'],
            substr($params['path'], 1)
        );
    }
}
