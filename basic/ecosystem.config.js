module.exports = {
    apps : [{
        name: 'mqtt_chat',
        script: 'app.js',
        watch: true,
        env: {
            'NODE_ENV': 'development',
        },
        env_production: {
            'NODE_ENV': 'production',
        }
    }]
};