import css from './admin.scss'

import { editorJs } from './editor.js'
import { editorJsHelper } from './editorJsHelper.js'

window.editorJsHelper = new editorJsHelper()

window.addEventListener('load', function () {
  window.editors = new editorJs().getEditors()
})
