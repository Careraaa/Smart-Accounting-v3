SMART ACCOUNTING V3 - SETUP INSTRUCTIONS

RUN IN TERMINAL
1. composer install
2. copy .env.example .env
3. php artisan key:generate

!--Make sure to have a ready database (smart_accounting_v3)--!

4. run xampp and open apache and mysql

!-- Run this in terminal again. --!
5. php artisan migrate
6. php artisan db:seed (or php artisan migrate:fresh --seed)
7. php artisan storage:link

-- FOR CLOUDFLARE --
8. edit .env file APP_URL, change into this: APP_URL=http://localhost
9. open cmd or powershell and paste this: winget install --id Cloudflare.cloudflared
10. open httpd-vhosts.conf file located at xampp\apache\conf\extra
11. replace the <Virtual Host> with this then save: 

<VirtualHost *:80>
    ##ServerAdmin webmaster@dummy-host.example.com
    DocumentRoot "C:/xampp/htdocs/Smart-Accounting-v3/public"
    ##ServerName smart-accounting.local
    <Directory "C:/xampp/htdocs/Smart-Accounting-v3/public">
        AllowOverride All 
        Require all granted 
    </Directory>
</VirtualHost>

12. go back to cmd run this: cloudflared tunnel --url http://localhost:80

--------------------------------------------------------------

NODEJS + VITE SETUP (for JS/SCSS bundling via Vite)

1. Download Node.js Installer
   - Go to: https://nodejs.org
   - Click the big green button on the left (LTS / Recommended for Most Users)
   - This downloads an .msi file (e.g., node-v22.xx.x-x64.msi)

2. Run the Installer
   - Double-click the downloaded .msi
   - Accept license → Next
   - Install location: default (C:\Program Files\nodejs\) → Next
   - Make sure checkboxes are ticked:
       * Automatically install necessary tools
       * Add to PATH (so node & npm work in terminal)
   - Click Install → allow UAC if prompted
   - Let the secondary installer finish (Python / Build Tools)

3. Close & Reopen VSCode (or terminal)
   - Run:
       node -v
       npm -v
   - You should see version numbers (e.g., v22.22.1 and npm 10.x.x)

4. Install Vite + Laravel Vite Plugin
   - In your project folder terminal, run:
       npm install --save-dev vite laravel-vite-plugin sass @tailwindcss/vite tailwindcss axios concurrently
   - This creates `node_modules` and updates `package.json`
   - Confirm installation:
       npm list --depth=0
       You should see vite, laravel-vite-plugin, sass, tailwindcss, etc.

--------------------------------------------------------------

FILES TO INCLUDE / COPY PASTE

1. package.json
----------------
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@tailwindcss/vite": "^4.0.0",
        "axios": "^1.11.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^2.1.0",
        "sass": "^1.98.0",
        "tailwindcss": "^4.0.0",
        "vite": "^7.3.1"
    }
}

2. vite.config.js
-------------------
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/scss/app.scss", // main SCSS entry
                "resources/js/app.js",     // main JS entry
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "localhost",
        hmr: {
            host: "localhost",
        },
    },
    resolve: {
        alias: {},
    },
});

--------------------------------------------------------------

USING VITE

- Every time you **change JS or SCSS**:
    1. Open terminal in project folder
    2. Run:
        npm run build
    3. This updates the bundled files in `public/build`
- **You do NOT need to restart Cloudflare tunnel**
- Just hard refresh your browser to see changes

--------------------------------------------------------------

SUMMARY

- Keep `package.json` and `vite.config.js` in Git
- Run `npm install` on any new machine after pulling
- Always run `npm run build` after editing JS/SCSS
- Cloudflare tunnel can stay running during builds