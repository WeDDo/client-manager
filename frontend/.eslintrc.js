module.exports = {
    // parserOptions: {
    //     parser: 'nuxt',
    //     requireConfigFile: false,
    // },
    extends: [
        // 'airbnb-base',
        'plugin:vue/recommended',
    ],
    rules: {
        curly: [2, 'all'],
        'linebreak-style': 0,
        'no-param-reassign': 0,
        indent: ['error', 4],
        'vue/html-indent': [
            'error',
            4,
            {
                baseIndent: 1,
            },
        ],
        'vue/component-name-in-template-casing': ['error', 'kebab-case', {
            registeredComponentsOnly: true,
            ignores: [],
        }],
    },
};

