export class Linguist {
}
Linguist.Get = (lang) => Linguist.LANGUAGES[lang];
Linguist.GetDisplayName = (lang) => Linguist.Get(lang).displayName;
Linguist.Detect = (name, acceptMultiple) => {
    const languages = Object.keys(Linguist.LANGUAGES);
    let possibleLanguages = [];
    for (let i = 0; i < Object.keys(Linguist.LANGUAGES).length; i++) {
        if (Linguist.Get(languages[i]).hasOwnProperty("extensions")) {
            Linguist.Get(languages[i]).extensions.forEach((extension) => {
                if (name.substr(-extension.length) === extension)
                    possibleLanguages.push(languages[i]);
            });
        }
        if (Linguist.Get(languages[i]).hasOwnProperty("fileNames")) {
            Linguist.Get(languages[i]).fileNames.forEach((fileName) => {
                if (name.substr(-fileName.length) === fileName)
                    possibleLanguages.push(languages[i]);
            });
        }
    }
    if (acceptMultiple)
        return possibleLanguages;
    else if (possibleLanguages.length === 0)
        return "file";
    else
        return possibleLanguages[0];
};
Linguist.GetTags = (name, isFile) => {
    let tags = [...Linguist.Detect(name, false)];
    if (isFile)
        tags.push("file");
    else
        tags.push("folder");
    return tags;
};
Linguist.LANGUAGES = {
    "aws": {
        type: "platform",
        displayName: "Amazon Web Services",
        iconName: "aws",
    },
    "azure": {
        type: "platform",
        displayName: "Azure",
        iconName: "azure",
    },
    "c": {
        type: "programming",
        displayName: "C",
        extensions: [
            ".c",
            ".cats",
            ".h",
            ".idc",
        ],
        iconName: "c",
    },
    "csharp": {
        type: "programming",
        displayName: "C#",
        extensions: [
            ".cs",
            ".cake",
            ".csx",
            ".csproj",
        ],
        iconName: "csharp",
    },
    "cpp": {
        type: "programming",
        displayName: "C++",
        extensions: [
            ".cpp",
            ".c++",
            ".cc",
            ".cp",
            ".cxx",
            ".h",
            ".h++",
            ".hh",
            ".hpp",
            ".hxx",
            ".inc",
            ".inl",
            ".ino",
            ".ipp",
            ".re",
            ".tcc",
            ".tpp",
        ],
        iconName: "cpp",
    },
    "css": {
        type: "markup",
        displayName: "CSS",
        extensions: [
            ".css",
        ],
        iconName: "css",
    },
    "csv": {
        type: "data",
        displayName: "CSV",
        extensions: [
            ".csv",
        ],
        iconName: "csv",
    },
    "cloud_firestore_security_rules": {
        type: "data",
        displayName: "Cloud Firestore Security Rules",
        fileNames: [
            "firestore.rules"
        ],
        iconName: "firebase",
    },
    "coffeescript": {
        type: "programming",
        displayName: "Coffee Script",
        extensions: [
            ".coffee",
            "._coffee",
            ".cake",
            ".cjsx",
            ".iced",
        ],
        fileNames: [
            "Cakefile"
        ],
        iconName: "coffeescript",
    },
    "digital-ocean": {
        type: "platform",
        displayName: "Digital Ocean",
        iconName: "digitalocean",
    },
    "docker": {
        type: "tool",
        displayName: "Docker",
        iconName: "docker",
    },
    "dockerfile": {
        type: "programming",
        displayName: "Dockerfile",
        extensions: [
            ".dockerfile",
        ],
        fileNames: [
            "Dockerfile"
        ],
        iconName: "docker",
    },
    "drupal": {
        type: "tool",
        displayName: "Drupal",
        iconName: "drupal",
    },
    "file": {
        type: "generic",
        displayName: "File",
        iconName: "file",
    },
    "firebase": {
        type: "platform",
        displayName: "Firebase",
        iconName: "firebase",
    },
    "folder": {
        type: "generic",
        displayName: "Folder",
        iconName: "folder",
    },
    "gcp": {
        type: "platform",
        displayName: "Google Cloud Platform",
        iconName: "gcp",
    },
    "go": {
        type: "programming",
        displayName: "Go",
        extensions: [
            ".go",
        ],
        iconName: "go",
    },
    "html": {
        type: "markup",
        displayName: "HTML",
        extensions: [
            ".html",
            ".htm",
            ".html.h1",
            ".inc",
            ".st",
            ".xht",
            ".xhtml",
        ],
        iconName: "html",
    },
    "java": {
        type: "programming",
        displayName: "Java",
        extensions: [
            ".java",
        ],
        iconName: "java",
    },
    "javascript": {
        type: "programming",
        displayName: "Javascript",
        extensions: [
            ".js",
            "._js",
            ".bones",
            ".es",
            ".es6",
            ".frag",
            ".gs",
            ".jake",
            ".jsb",
            ".jscad",
            ".jsfl",
            ".jsm",
            ".jss",
            ".mjs",
            ".njs",
            ".pac",
            ".sjs",
            ".ssjs",
            ".xsjs",
            ".xsjslib",
        ],
        fileNames: [
            "Jakefile"
        ],
        iconName: "javascript",
    },
    "kubernetes": {
        type: "tool",
        displayName: "Kubernetes",
        iconName: "kubernetes",
    },
    "mongodb": {
        type: "database",
        displayName: "mongoDB",
        iconName: "mongodb",
    },
    "mysql": {
        type: "database",
        displayName: "MySQL",
        iconName: "mysql",
    },
    "nodejs": {
        type: "server",
        displayName: "NodeJS",
        iconName: "nodejs",
    },
    "postgresql": {
        type: "database",
        displayName: "PostgreSQL",
        iconName: "postgresql",
    },
    "sql": {
        type: "data",
        displayName: "SQL",
        extensions: [
            ".sql",
            ".cql",
            ".ddl",
            ".inc",
            ".mysql",
            ".prc",
            ".tab",
            ".udf",
            ".viw",
        ],
        iconName: "mysql",
    },
    "php": {
        type: "programming",
        displayName: "PHP",
        extensions: [
            ".php",
            ".aw",
            ".ctp",
            ".fcgi",
            ".inc",
            ".php3",
            ".php4",
            ".php5",
            ".phps",
            ".phpt",
        ],
        fileNames: [
            ".php",
            ".php_cs",
            ".php_cs.dist",
            "Phakefile",
        ],
        iconName: "php",
    },
    "python": {
        type: "programming",
        displayName: "Python",
        extensions: [
            ".py",
            ".bzl",
            ".cgi",
            ".fcgi",
            ".gyp",
            ".gypi",
            ".lmi",
            ".py3",
            ".pyde",
            ".pyi",
            ".pyp",
            ".pyt",
            ".pyw",
            ".rpy",
            ".spec",
            ".tac",
            ".wsgi",
            ".xpy",
        ],
        fileNames: [
            ".gclient",
            "BUCK",
            "BUILD",
            "BUILD.bazel",
            "DEPS",
            "SConscript",
            "SConstruct",
            "Snakefile",
            "WORKSPACE",
            "wscript",
        ],
        iconName: "python",
    },
    "ruby": {
        type: "programming",
        displayName: "Ruby",
        extensions: [
            ".rb",
            ".builder",
            ".eye",
            ".fcgi",
            ".gemspec",
            ".god",
            ".jbuilder",
            ".mspec",
            ".pluginspec",
            ".podspec",
            ".rabl",
            ".rake",
            ".rbuild",
            ".rbw",
            ".rbx",
            ".ru",
            ".ruby",
            ".spec",
            ".thor",
            ".watchr",
        ],
        fileNames: [
            ".irbrc",
            ".pryrc",
            "Appraisals",
            "Berksfile",
            "Brewfile",
            "Buildfile",
            "Capfile",
            "Dangerfile",
            "Deliverfile",
            "Fastfile",
            "Gemfile",
            "Gemfile.lock",
            "Guardfile",
            "Jarfile",
            "Mavenfile",
            "Podfile",
            "Puppetfile",
            "Rakefile",
            "Snapfile",
            "Thorfile",
            "Vagrantfile",
            "buildfile",
        ],
        iconName: "ruby",
    },
    "rust": {
        type: "programming",
        displayName: "Rust",
        extensions: [
            ".rs",
            ".rs.in",
        ],
        iconName: "ruse",
    },
    "wordpress": {
        type: "tool",
        displayName: "WordPress",
        iconName: "wordpress",
    },
};
//# sourceMappingURL=Linguist.js.map