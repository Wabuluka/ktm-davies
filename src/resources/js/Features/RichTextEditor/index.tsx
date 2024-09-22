/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import { LexicalComposer } from '@lexical/react/LexicalComposer';
import * as React from 'react';
import './index.css';

import { $generateNodesFromDOM } from '@lexical/html';
import { InitialEditorStateType } from '@lexical/react/LexicalComposer';
import { $getRoot, $insertNodes, TextNode } from 'lexical';
import { Editor, EditorProps } from './Editor';
import { SharedHistoryContext } from './context/SharedHistoryContext';
import PlaygroundNodes from './nodes/PlaygroundNodes';
import { TableContext } from './plugins/TablePlugin';
import PlaygroundEditorTheme from './themes/PlaygroundEditorTheme';
import { ExtendedTextNode } from '@/Features/RichTextEditor/plugins/ExtendedTextPlugin';

type Props = EditorProps & {
  defaultValue?: string;
};

export const RichTextEditor: React.FC<Props> = ({ defaultValue, ...props }) => {
  const prepopulatedRichText: InitialEditorStateType = (editor) => {
    if (defaultValue) {
      const parser = new DOMParser();
      const dom = parser.parseFromString(defaultValue, 'text/html');
      const nodes = $generateNodesFromDOM(editor, dom);
      $getRoot().select();
      $insertNodes(nodes);
    }
  };

  const initialConfig = {
    editorState: prepopulatedRichText,
    namespace: 'Playground',
    nodes: [
      ...PlaygroundNodes,
      ExtendedTextNode,
      {
        replace: TextNode,
        with: (node: TextNode) => new ExtendedTextNode(node.__text),
      },
    ],
    onError: (error: Error) => {
      throw error;
    },
    theme: PlaygroundEditorTheme,
  };

  return (
    <LexicalComposer initialConfig={initialConfig}>
      <SharedHistoryContext>
        <TableContext>
          <div className="editor-shell">
            <Editor {...props} />
          </div>
        </TableContext>
      </SharedHistoryContext>
    </LexicalComposer>
  );
};

export default RichTextEditor;
