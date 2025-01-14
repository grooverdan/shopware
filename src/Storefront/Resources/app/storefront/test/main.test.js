/** @deprecated tag:v6.6.0 - Entry point containing async imports will be turned into the main.js. */
import 'src/main-async';
import NativeEventEmitter from 'src/helper/emitter.helper';

describe('Storefront main entry point', () => {
    beforeEach(() => {
        global.themeJsPublicPath = 'public/theme/#theme-hash#';
    });

    it('should have all needed window properties', () => {
        expect(window.bootstrap).toBeDefined();
        expect(typeof window.bootstrap).toBe('object');

        expect(window.eventEmitter).toBeInstanceOf(NativeEventEmitter);

        expect(window.Feature).toBeDefined();
        expect(typeof window.Feature).toBe('function');

        expect(window.PluginManager).toBeDefined();
        expect(window.PluginBaseClass).toBeDefined();
    });
});
