import { Config } from 'jest';
import { compilerOptions } from './tsconfig.json';
import { pathsToModuleNameMapper } from 'ts-jest';

const config: Config = {
    verbose: true,
    detectLeaks: true,
    detectOpenHandles: true,
    errorOnDeprecated: true,
    preset: 'jest-preset-angular',
    testEnvironment: 'jsdom',
    // @todo this is disabled temprarly and put to angular.json, waiting for @angular-builders/jest to support zoneless
    // @todo see setup-jest.ts
    // setupFilesAfterEnv: ['<rootDir>/setup-jest.ts'],
    transform: {
        '^.+\\.(ts|js|html)$': 'jest-preset-angular',
    },
    moduleFileExtensions: ['ts', 'html', 'js', 'json'],
    testPathIgnorePatterns: [
        '<rootDir>/node_modules/',
        '<rootDir>/dist/',
    ],
    moduleNameMapper: pathsToModuleNameMapper(compilerOptions.paths || {}, {
        prefix: `<rootDir>/${compilerOptions.baseUrl}/`,
    }),
    testMatch: ['**/*.spec.ts']
};

export default config;