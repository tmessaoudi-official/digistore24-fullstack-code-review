// @todo when jest-preset-angular and @angular-builders/jest support zoneless, remove this file and just uncomment setupFilesAfterEnv in jest.config.ts and remove setupFilesAfterEnv from angular.json
// @todo wait for jest-preset-angular and @angular-builders/jest to support zoneless
import { setupZonelessTestEnv } from 'jest-preset-angular/setup-env/zoneless';

setupZonelessTestEnv();

// import "@angular-builders/jest/dist/jest-config/setup.js";
import "@angular-builders/jest/dist/global-mocks/style-transform.js";
import "@angular-builders/jest/dist/global-mocks/match-media.js";