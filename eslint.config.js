const wpPlugin = require("@wordpress/eslint-plugin");

module.exports = [
	{
		ignores: ["**/build/**", "**/node_modules/**", "**/vendor/**"],
	},
	...wpPlugin.configs.recommended,
];
