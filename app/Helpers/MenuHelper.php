<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'subItems' => [
                    ['name' => 'Ecommerce', 'path' => '/'],
                ],
            ],
            [
                'icon' => 'calendar',
                'name' => 'Calendar',
                'path' => '/calendar',
            ],
            [
                'icon' => 'user-profile',
                'name' => 'User Profile',
                'path' => '/profile',
            ],
            [
                'name' => 'Forms',
                'icon' => 'forms',
                'subItems' => [
                    ['name' => 'Form Elements', 'path' => '/form-elements', 'pro' => false],
                ],
            ],
            [
                'name' => 'Tables',
                'icon' => 'tables',
                'subItems' => [
                    ['name' => 'Basic Tables', 'path' => '/basic-tables', 'pro' => false]
                ],
            ],
            [
                'name' => 'Pages',
                'icon' => 'pages',
                'subItems' => [
                    ['name' => 'Blank Page', 'path' => '/blank', 'pro' => false],
                    ['name' => '404 Error', 'path' => '/error-404', 'pro' => false]
                ],
            ],
        ];
    }

    public static function getOthersItems()
    {
        return [
            [
                'icon' => 'charts',
                'name' => 'Charts',
                'subItems' => [
                    ['name' => 'Line Chart', 'path' => '/line-chart', 'pro' => false],
                    ['name' => 'Bar Chart', 'path' => '/bar-chart', 'pro' => false]
                ],
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'UI Elements',
                'subItems' => [
                    ['name' => 'Alerts', 'path' => '/alerts', 'pro' => false],
                    ['name' => 'Avatar', 'path' => '/avatars', 'pro' => false],
                    ['name' => 'Badge', 'path' => '/badge', 'pro' => false],
                    ['name' => 'Buttons', 'path' => '/buttons', 'pro' => false],
                    ['name' => 'Images', 'path' => '/image', 'pro' => false],
                    ['name' => 'Videos', 'path' => '/videos', 'pro' => false],
                ],
            ],
            [
                'icon' => 'authentication',
                'name' => 'Authentication',
                'subItems' => [
                    ['name' => 'Sign In', 'path' => '/signin', 'pro' => false],
                    ['name' => 'Sign Up', 'path' => '/signup', 'pro' => false],
                ],
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Menu',
                'items' => self::getMainNavItems()
            ],
            [
                'title' => 'Others',
                'items' => self::getOthersItems()
            ]
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',
            // fallback icon omitted for brevity
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
