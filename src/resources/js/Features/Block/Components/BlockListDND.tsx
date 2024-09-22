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
import { EditCustomBlockDrawer } from './EditCustomBlockDrawer';
import { DragDropContext, Draggable, Droppable } from 'react-beautiful-dnd';
import { BlockListItemDND } from '@/Features/Block/Components/BlockListItemDND';

type Props = {
  blocks: BlockOnBookForm[];
  onBookStoreBlockEdit: (block: BlockOnBookForm) => void;
  onEbookStoreBlockEdit: (block: BlockOnBookForm) => void;
  onBenefitBlockEdit: (block: BlockOnBookForm) => void;
  onRelatedBlockEdit: (block: BlockOnBookForm) => void;
  onStoryBlockEdit: (block: BlockOnBookForm) => void;
  onCharacterBlockEdit: (block: BlockOnBookForm) => void;
} & Omit<TableProps, 'children'>;

export function BlockListDND({
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
        <DragDropContext
          onDragEnd={(result) => {
            if (!result.destination) return;
            if (result.destination.index === result.source.index) return;
            blocks.forEach(sortedBlock => {
              console.log('_____',sortedBlock.sort)
            });
            const newBlocks = [...blocks];
            newBlocks.splice(result.source.index, 1);
            newBlocks.splice(result.destination.index, 0, blocks[result.source.index], );
            console.log(newBlocks.sort);
            blocks = newBlocks;
          }}
        >
          <Droppable droppableId="blocks">
            {(provided) => (
              <Tbody ref={provided.innerRef} {...provided.droppableProps}>
                {blocks.map((block, i) => (
                  <Draggable
                    key={block.sort}
                    index={i}
                    draggableId={block.id.toString()}
                  >
                    {(provided) => (
                      <tr
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                        ref={provided.innerRef}
                        // style={{ ...provided.draggableProps.style,  backgroundColor: '#f8f8f8' }}
                      >
                        <BlockListItemDND
                          key={block.sort}
                          block={block}
                          onEdit={createOnEditHandler(block)}
                        />
                      </tr>
                    )}
                  </Draggable>
                ))}
                {provided.placeholder}
              </Tbody>
            )}
          </Droppable>
        </DragDropContext>
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
