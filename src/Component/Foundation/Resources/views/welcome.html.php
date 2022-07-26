<!DOCTYPE html>
<html lang="en" class="font-sans antialiased">
<head>
    <meta charset="utf-8"/>
    <title>Welcome to Pandawa!</title>
    <base href="/"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <meta name="theme-color" content="#64a1d9"/>

    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>

    <link as="font" crossorigin="" href="https://fonts.gstatic.com/s/manrope/v8/xn7gYHE41ni1AdIRggexSvfedN4.woff2"
          rel="preload" type="font/woff2"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
          rel="stylesheet"/>

    <style>
        * {
            font-family: Inter, 'sans-serif';
        }

        a {
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        @media (max-height: 528px) {
            body {
                padding-top: 20px;
                padding-bottom: 20px;
                height: auto;
            }
        }

        .container {
            width: 100%;
        }

        @media (min-width: 768px) {
            .container {
                width: 768px;
            }
        }

        @media (min-width: 992px) {
            .container {
                width: 992px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                width: 1200px;
            }
        }

        .mx-auto {
            margin: 0 auto;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .grid {
            display: grid;
        }

        @media (max-width: 768px) {
            .grid.sm\:block {
                display: block;
            }

            .sm\:mt-8 {
                margin-top: 32px;
            }
        }

        .grid-cols-6 {
            grid-template-columns: repeat(6, 1fr);
        }

        .gap-14 {
            grid-column-gap: 56px;
            grid-row-gap: 56px;
        }

        .col-span-2 {
            grid-column: span 2 / span 2;
        }

        .col-span-4 {
            grid-column: span 4 / span 4;
        }

        .rounded-xl {
            border-radius: 12px;
        }

        .border {
            border-style: solid;
            border-width: 1px;
        }

        .border-grey {
            border-color: #ddd;
        }

        .w-full {
            width: 100%;
        }

        .text-sm {
            font-size: 14px;
        }

        .text-base {
            font-size: 16px;
        }

        .text-3xl {
            font-size: 40px;
        }

        h1, h2, h3, h4, h5, h6, p {
            margin: 0;
            padding: 0;
        }

        .font-medium {
            font-weight: 400;
        }

        .font-semibold {
            font-weight: 500;
        }

        .mt-8 {
            margin-top: 32px;
        }

        .text-grey-600 {
            color: #575757;
        }

        .py-4 {
            padding-top: 16px;
            padding-bottom: 16px;
        }

        .px-6 {
            padding-left: 24px;
            padding-right: 24px;
        }

        .bg-primary {
            background: #64a1d9;
        }

        .text-blank {
            color: white;
        }

        .ml-6 {
            margin-left: 24px;
        }

        .h-6 {
            height: 24px;
        }

        .w-6 {
            width: 24px;
        }

        .text-primary {
            color: #64a1d9;
        }

        a {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container mx-auto px-6">
    <div class="grid grid-cols-6 gap-14 items-center sm:block">
        <div class="col-span-2">
            <div class="border border-grey w-full" style="height: 400px;">
            </div>
        </div>
        <div class="col-span-4 sm:mt-8">
            <h4 class="text-3xl">Pandawa Framework <?= $version ?></h4>
            <div class="mt-8 text-base text-grey-600" style="line-height: 200%;">
                Pandawa Framework is a modern PHP framework built based on Laravel. Pandawa Framework
                is plain, fast and good for building microservices. Pandawa Framework is 100% support for
                Laravel packages.
            </div>
            <div class="mt-8 flex items-center">
                <a href="https://github.com/pandawa/pandawa" target="_blank"
                   class="bg-primary py-4 px-6 text-blank font-medium text-sm">Open on GitHub</a>
                <a href="https://github.com/pandawa/pandawa" target="_blank"
                   class="ml-6 font-semibold text-sm flex items-center">
                    <span>Documentation</span>
                    <label class="text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </label>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
