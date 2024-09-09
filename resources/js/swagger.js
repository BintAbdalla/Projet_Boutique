import swaggerUI from 'swagger-ui';
import 'swagger-ui/dist/swagger-ui.css';


swaggerUI({
    url: '/api.yaml',
    dom_id: '#swagger-api',
    // presets: [
    //   SwaggerUIBundle({
    //     layout: 'StandaloneLayout'
    //   }),
    //   SwaggerUIStandalonePreset()
    // ]
});
