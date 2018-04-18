const isProduction = process.env.NODE_ENV === 'production'

module.exports = {
  plugins: isProduction ? [
    require('autoprefixer'),
    require('cssnano'),
  ] : [],
}
