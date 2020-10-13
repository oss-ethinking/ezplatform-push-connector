const path = require('path');

module.exports = (Encore) => {
    Encore
        .addEntry('ezplatform-push-connector-archive-js', [
            path.resolve(__dirname, '../public/js/scripts/archive.js'),
        ])
        .addEntry('ezplatform-push-connector-channel-js', [
            path.resolve(__dirname, '../public/js/scripts/channel.js'),
        ])
        .addEntry('ezplatform-push-connector-settings-js', [
            path.resolve(__dirname, '../public/js/scripts/settings.js'),
        ])
        .addStyleEntry('ezplatform-push-connector-main-css', [
            path.resolve(__dirname, '../public/scss/main.scss'),
        ])
};