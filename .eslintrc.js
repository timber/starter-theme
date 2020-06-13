module.exports = {
  "env": {
      "commonjs": true,
      "es2020": true,
      "node": true
  },
  "extends": "eslint:recommended",
  "parserOptions": {
      "ecmaVersion": 11
  },
  "globals": {
      "jQuery": "writable"
  },
  "rules": {
      "linebreak-style": [
          "error",
          "unix"
      ],
      "semi": [
          "error",
          "always"
      ]
   }
};
