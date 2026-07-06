<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Pwa extends Controller
{
    public function manifest()
    {
        $base = rtrim(base_url(), '/') . '/';

        $manifest = [
            'id'               => $base,
            'name'             => 'GestiStore',
            'short_name'       => 'GestiStore',
            'description'      => 'Gestion de stock et ventes',
            'lang'             => 'fr',
            'start_url'        => $base,
            'scope'            => $base,
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#101418',
            'theme_color'      => '#22c55e',
            'icons'            => [
                [
                    'src'     => base_url('icons/icon-192.png'),
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src'     => base_url('icons/icon-512.png'),
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src'     => base_url('icons/icon-512.png'),
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
        ];

        return $this->response
            ->setJSON($manifest)
            ->setHeader('Content-Type', 'application/manifest+json; charset=utf-8')
            ->setHeader('Cache-Control', 'no-cache');
    }
}
