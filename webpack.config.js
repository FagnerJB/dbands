const path = require('path')
const TerserPlugin = require('terser-webpack-plugin')

module.exports = {
   entry: './dbwp/wp-content/themes/dbands_OO/assets_dev/index.ts',
   module: {
      rules: [
         {
            test: /\.tsx?$/,
            use: 'ts-loader',
            exclude: /node_modules/,
         },
      ],
   },
   resolve: {
      extensions: ['.tsx', '.ts', '.js'],
   },
   output: {
      filename: 'script.js',
      path: path.resolve(
         __dirname,
         'dbwp',
         'wp-content',
         'themes',
         'dbands_OO',
         'assets'
      ),
   },
   optimization: {
      minimize: true,
      minimizer: [new TerserPlugin()],
   },
   mode: 'production',
}
