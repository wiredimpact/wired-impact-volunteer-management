// ***********************************************************
// This support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

// Ignore benign uncaught exceptions that come from application code (WordPress
// core or the theme) rather than the plugin, so they don't fail our tests.
Cypress.on('uncaught:exception', (err) => {

    // Occurs when using Gravity Forms merge tag dropdowns.
    if ((err.message.includes("ResizeObserver loop limit exceeded"))) {
        return false;
    }

    // "AbortError: Transition was skipped" is a browser-native View Transitions
    // API rejection, surfaced by WordPress core on the block-theme frontend when
    // a navigation's view transition is skipped. It is not thrown by this plugin
    // (it still fires with the plugin deactivated), so ignore it here.
    if ((err.message.includes("Transition was skipped"))) {
        return false;
    }
});