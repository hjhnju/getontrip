exports.input = __dirname;

var path = require( 'path' );

var date = new Date();
var time = ''
    + date.getFullYear()
    + ('' + (date.getMonth() + 101)).substr(1)
    + ('' + (date.getDate() + 100)).substr(1);
time='20160112';
exports.output = path.resolve( __dirname, '../htdocs/v1', time + 'x2' );

exports.getProcessors = function () {
    var lessProcessor = new LessCompiler({
        files: [
            'src/home/css/index.less',
            'src/home/css/interest.less',
            'src/topic/css/detail.less',
            'src/topic/css/mdetail.less',
            'src/m/sight/css/map.less',
            'src/m/sight/css/guide.less',
            'src/m/sight/css/nearby.less',
            'src/m/sight/css/landscape.less'

        ]
    });
    var cssProcessor = new CssCompressor({
        files: [
            'src/home/css/index.less', 
            'src/home/css/interest.less',
            'src/topic/css/detail.less',
            'src/topic/css/mdetail.less',
            'src/m/sight/css/map.less',
            'src/m/sight/css/guide.less',
            'src/m/sight/css/nearby.less',
            'src/m/sight/css/landscape.less'
        ]
    });
    var moduleProcessor = new ModuleCompiler({
        files: [
            '*.js',
            '!~src/common/extra/jquery.js',
            '!~src/common/extra/jquery.qrcode.js',
            '!~src/common/extra/jquery.zclip.js',
            '!~src/common/extra/esl.js',
            '!~src/common/extra/Datepicker.js' 
        ]
    });
    var jsProcessor = new JsCompressor({
        files: [ 
            'src/home/index.js',
            'src/topic/detail.js',
            'src/m/sight/map.js',
            'src/m/sight/guide.js',
            'src/m/sight/nearby.js',
            'src/m/sight/landscape.js'
        ]
    });
    var html2JsProcessor = new Html2JsCompiler({
        mode: 'compress',
        extnames: [ 'tpl' ],
        combine: true
    });
    var html2jsClearPorcessor = new Html2JsCompiler({
        extnames: 'tpl',
        clean: true
    });
    var pathMapperProcessor = new PathMapper();

    return {
        'release': [ lessProcessor, html2JsProcessor, moduleProcessor,
            html2jsClearPorcessor, pathMapperProcessor ],
        'default': [
            lessProcessor, cssProcessor, html2JsProcessor, moduleProcessor,
            html2jsClearPorcessor, jsProcessor, pathMapperProcessor
        ]
    };
};

exports.exclude = [
    //'tool',
    'doc',
    'test',
    'entry',
    'output',
    'mock',
    'node_modules',
    'module.conf',
    'package.json',
    '*.sh',
    'README.md',
    'dep/packages.manifest',
    'dep/*/*/test',
    'dep/*/*/doc',
    'dep/*/*/demo',
    'dep/*/*/tool',
    'dep/*/*/*.md',
    'dep/*/*/package.json',
    'edp-*',
    '.edpproj',
    '.svn',
    '.git',
    '.gitignore',
    '.idea',
    '.project',
    'Desktop.ini',
    'Thumbs.db',
    '.DS_Store',
    '*.tmp',
    '*.bak',
    '*.swp'
];

exports.injectProcessor = function ( processors ) {
    for ( var key in processors ) {
        global[ key ] = processors[ key ];
    }
};

