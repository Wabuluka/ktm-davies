import {
  Table,
  TableProps,
  Tbody,
  Td,
  Thead,
  Tr,
  useDisclosure,
} from '@chakra-ui/react';
import { useState, useEffect } from 'react';
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
  const [isDragging, setIsDragging] = useState(false);
  const [editingCustomBlock, setEditingCustomBlock] =
    useState<BlockOnBookForm>();

  const [blockList, setBlockList] = useState<BlockOnBookForm[]>([]);

  useEffect(() => {
    const savedBlocks = localStorage.getItem('blockList');
    if (savedBlocks) {
      setBlockList(JSON.parse(savedBlocks));
    } else {
      setBlockList(blocks);
    }
  }, [blocks]);

  const saveBlockList = (updatedBlocks: BlockOnBookForm[]) => {
    localStorage.setItem('blockList', JSON.stringify(updatedBlocks));
  };

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
        throw new Error('Invalid block type');
      }
    };
  }

  const handleOnDragEnd = (result:any) => {
    const { destination, source } = result;
    if (!destination || destination.index === source.index) return;

    const reorderedBlocks = Array.from(blockList);
    const [movedBlock] = reorderedBlocks.splice(source.index, 1);
    reorderedBlocks.splice(destination.index, 0, movedBlock);
    setBlockList(reorderedBlocks);
    saveBlockList(reorderedBlocks);
  };

  const handleOnBeforeDragStart = () => {
    setIsDragging(true);
  };

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

          onDragEnd={handleOnDragEnd}
          onBeforeDragStart={handleOnBeforeDragStart}
        >
          <Droppable droppableId="blocks"
          >
            {(provided) => (
              <Tbody ref={provided.innerRef} {...provided.droppableProps}>
                {blockList.map((block, i) => (
                  <Draggable
                    key={block.id}
                    index={i}
                    draggableId={block.id.toString()}
                  >
                    {(provided,) => (
                      <Tr
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                        ref={provided.innerRef}
                        style={{
                          ...provided.draggableProps.style,
                          display: 'table-row',

                        }}
                      >
                        <BlockListItemDND
                          key={block.id}
                          block={block}
                          isDragging={isDragging}
                          onEdit={createOnEditHandler(block)}
                        />
                      </Tr>
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
