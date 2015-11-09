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
