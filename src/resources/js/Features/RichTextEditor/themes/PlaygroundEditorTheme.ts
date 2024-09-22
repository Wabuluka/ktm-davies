/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type { EditorThemeClasses } from 'lexical';

import './PlaygroundEditorTheme.css';

const theme: EditorThemeClasses = {
  blockCursor: 'PlaygroundEditorTheme__blockCursor',
  characterLimit: 'rte__characterLimit',
  code: 'rte__code',
  codeHighlight: {
    atrule: 'rte__tokenAttr',
    attr: 'rte__tokenAttr',
    boolean: 'rte__tokenProperty',
    builtin: 'rte__tokenSelector',
    cdata: 'rte__tokenComment',
    char: 'rte__tokenSelector',
    class: 'rte__tokenFunction',
    'class-name': 'rte__tokenFunction',
    comment: 'rte__tokenComment',
    constant: 'rte__tokenProperty',
    deleted: 'rte__tokenProperty',
    doctype: 'rte__tokenComment',
    entity: 'rte__tokenOperator',
    function: 'rte__tokenFunction',
    important: 'rte__tokenVariable',
    inserted: 'rte__tokenSelector',
    keyword: 'rte__tokenAttr',
    namespace: 'rte__tokenVariable',
    number: 'rte__tokenProperty',
    operator: 'rte__tokenOperator',
    prolog: 'rte__tokenComment',
    property: 'rte__tokenProperty',
    punctuation: 'rte__tokenPunctuation',
    regex: 'rte__tokenVariable',
    selector: 'rte__tokenSelector',
    string: 'rte__tokenSelector',
    symbol: 'rte__tokenProperty',
    tag: 'rte__tokenProperty',
    url: 'rte__tokenOperator',
    variable: 'rte__tokenVariable',
  },
  embedBlock: {
    base: 'rte__embedBlock',
    focus: 'rte__embedBlockFocus',
  },
  hashtag: 'rte__hashtag',
  heading: {
    h1: 'rte__h1',
    h2: 'rte__h2',
    h3: 'rte__h3',
    h4: 'rte__h4',
    h5: 'rte__h5',
    h6: 'rte__h6',
  },
  image: 'editor-image',
  link: 'rte__link',
  list: {
    listitem: 'rte__listItem',
    listitemChecked: 'rte__listItemChecked',
    listitemUnchecked: 'rte__listItemUnchecked',
    nested: {
      listitem: 'rte__nestedListItem',
    },
    olDepth: ['rte__ol1', 'rte__ol2', 'rte__ol3', 'rte__ol4', 'rte__ol5'],
    ul: 'rte__ul',
  },
  ltr: 'rte__ltr',
  mark: 'rte__mark',
  markOverlap: 'rte__markOverlap',
  paragraph: 'rte__paragraph',
  quote: 'rte__quote',
  rtl: 'rte__rtl',
  table: 'rte__table',
  tableAddColumns: 'rte__tableAddColumns',
  tableAddRows: 'rte__tableAddRows',
  tableCell: 'rte__tableCell',
  tableCellActionButton: 'rte__tableCellActionButton',
  tableCellActionButtonContainer: 'rte__tableCellActionButtonContainer',
  tableCellEditing: 'rte__tableCellEditing',
  tableCellHeader: 'rte__tableCellHeader',
  tableCellPrimarySelected: 'rte__tableCellPrimarySelected',
  tableCellResizer: 'rte__tableCellResizer',
  tableCellSelected: 'rte__tableCellSelected',
  tableCellSortedIndicator: 'rte__tableCellSortedIndicator',
  tableResizeRuler: 'rte__tableCellResizeRuler',
  tableSelected: 'rte__tableSelected',
  text: {
    bold: 'rte__textBold',
    code: 'rte__textCode',
    italic: 'rte__textItalic',
    strikethrough: 'rte__textStrikethrough',
    subscript: 'rte__textSubscript',
    superscript: 'rte__textSuperscript',
    underline: 'rte__textUnderline',
    underlineStrikethrough: 'rte__textUnderlineStrikethrough',
  },
};

export default theme;
