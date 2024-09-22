import {
  Table,
  TableProps,
  Tbody,
  Td,
  Thead,
  Tr,
  useDisclosure,
} from '@chakra-ui/react';
import { useState } from 'react';
import { useBlockTypes } from '../Hooks/useBlockTypes';
import { BlockOnBookForm } from '../Types';
import { BlockListItem } from './BlockListItem';
import { EditCustomBlockDrawer } from './EditCustomBlockDrawer';

type Props = {
  blocks: BlockOnBookForm[];
  onBookStoreBlockEdit: (block: BlockOnBookForm) => void;
  onEbookStoreBlockEdit: (block: BlockOnBookForm) => void;
  onBenefitBlockEdit: (block: BlockOnBookForm) => void;
  onRelatedBlockEdit: (block: BlockOnBookForm) => void;
  onStoryBlockEdit: (block: BlockOnBookForm) => void;
  onCharacterBlockEdit: (block: BlockOnBookForm) => void;
} & Omit<TableProps, 'children'>;

export function BlockList({
  blocks,
  onBookStoreBlockEdit,
  onEbookStoreBlockEdit,
  onBenefitBlockEdit,
  onRelatedBlockEdit,
  onStoryBlockEdit,
  onCharacterBlockEdit,
  ...props
}: Props) {
  const {
    isBookStoreBlock,
    isEbookStoreBlock,
    isBenefitBlock,
    isRelatedBlock,
    isStoryBlock,
    isCharacterBlock,
    isCustomBlock,
  } = useBlockTypes();
  const { isOpen, onClose, onOpen } = useDisclosure();
  const [editingCustomBlock, setEditingCustomBlock] =
    useState<BlockOnBookForm>();

  function createOnEditHandler(block: BlockOnBookForm) {
    return (formData: BlockOnBookForm) => {
      if (isBookStoreBlock(block.type_id)) {
        onBookStoreBlockEdit(formData);
      } else if (isEbookStoreBlock(block.type_id)) {
        onEbookStoreBlockEdit(formData);
      } else if (isBenefitBlock(block.type_id)) {
        onBenefitBlockEdit(formData);
      } else if (isRelatedBlock(block.type_id)) {
        onRelatedBlockEdit(formData);
      } else if (isStoryBlock(block.type_id)) {
        onStoryBlockEdit(formData);
      } else if (isCharacterBlock(block.type_id)) {
        onCharacterBlockEdit(formData);
      } else if (isCustomBlock(block.type_id)) {
        setEditingCustomBlock(formData);
        onOpen();
      } else {
        throw new Error('ブロックの種別が不正です');
      }
    };
  }

  return (
    <>
      <Table {...props}>
        <Thead>
          <Tr>
            <Td w={1} whiteSpace="nowrap">
              Displayed
            </Td>
            <Td>Content</Td>
            <Td w={1}>Operation</Td>
          </Tr>
        </Thead>
        <Tbody>
          {blocks.map((block, i) => (
            <BlockListItem
              key={block.id}
              block={block}
              isFirst={i === 0}
              isLast={i === blocks.length - 1}
              onEdit={createOnEditHandler(block)}
            />
          ))}
        </Tbody>
      </Table>
      {!!editingCustomBlock && (
        <EditCustomBlockDrawer
          customBlock={editingCustomBlock}
          isOpen={isOpen}
          onClose={onClose}
        />
      )}
    </>
  );
}
