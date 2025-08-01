module.exports = {
  css: {
    loaderOptions: {
      sass: {
        sassOptions: {
          indentedSyntax: true,
        },
      },
    },
  },
  // Docker-specific configuration for hot reloading
  devServer: {
    host: "0.0.0.0",
    port: 8080,
    hot: true,
    liveReload: true,
    client: {
      webSocketURL: "ws://localhost:8080/ws",
    },
  },
  // Enable polling for file changes in Docker
  configureWebpack: {
    watchOptions: {
      poll: 1000,
      ignored: /node_modules/,
    },
  },
};
