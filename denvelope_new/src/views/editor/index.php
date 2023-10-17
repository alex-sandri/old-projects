<div class="show-file">
    <div class="editor-head">
        <h1 class="name"></h1>
        <div>
            <button class="menu">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <button class="close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="editor" id="editor"></div>
</div>
<script src="editor/dev/vs/loader.js"></script>
<script>require.config({paths: {"vs": "editor/dev/vs"}});</script>
<script defer src="editor/dev/vs/editor/editor.main.js"></script>
<script defer src="editor/dev/vs/editor/editor.main.nls.js"></script>