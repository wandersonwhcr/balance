/**
 * Aplicativo
 */
$.application = function () {
    /**
     * Configurações
     * @type object
     */
    this.configs = {};
};

/**
 * Configurar Configurações
 *
 * @param  object configs Valores para Configuração
 * @return self   Próprio Objeto para Encadeamento
 */
$.application.setConfigs = function (configs) {
    this.configs = configs;
    return this;
};

/**
 * Apresentar Configurações
 *
 * @return object Valores Configurados
 */
$.application.getConfigs = function () {
    return this.configs;
};

/**
 * Apresentar Configuração
 *
 * @param  string name Nome da Configuração
 * @return mixed  Valor Configurado
 */
$.application.getConfig = function (name) {
    return this.configs[name];
};

/**
 * Apresentar o Caminho Base do Sistema
 *
 * @param  string url URL para Concatenação
 * @return string Resultado Esperado
 */
$.application.basePath = function (url) {
    // Concatenação Necessária
    return this.getConfig('basePath') + (url ? url : '/');
};
