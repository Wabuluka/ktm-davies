import { BlockFormData } from '@/Features/Block/Types';
import { useSetBookFormData } from '@/Features/Book/Context/BookFormContext';
import { uniqueId } from 'lodash';
import { useCallback } from 'react';
import { BookFormData } from '../Types';

export function useBlockDispatcher() {
  const { setData } = useSetBookFormData();

  const updateBlockOnBookForm = useCallback(
    (callback: (blocks: BookFormData['blocks']) => BookFormData['blocks']) => {
      setData(({ blocks, ...rest }) => ({
        ...rest,
        blocks: callback(blocks),
      }));
    },
    [setData],
  );

  const addCustomBlock = useCallback(() => {
    updateBlockOnBookForm(({ upsert, deleteIds }) => ({
      upsert: [
        ...upsert,
        {
          id: `+${uniqueId()}`,
          type_id: '9',
          custom_title: 'Custom Block',
          custom_content: '',
          sort: upsert.length + 1,
          displayed: false,
        },
      ],
      deleteIds,
    }));
  }, [updateBlockOnBookForm]);

  const updateBlock = useCallback(
    (block: BlockFormData, blockId: string) => {
      updateBlockOnBookForm(({ upsert, deleteIds }) => ({
        upsert: upsert.map((added) =>
          added.id === blockId
            ? {
                ...added,
                custom_title: block.custom_title,
                custom_content: block.custom_content,
              }
            : added,
        ),
        deleteIds,
      }));
    },
    [updateBlockOnBookForm],
  );

  const deleteBlock = useCallback(
    (blockId: string) => {
      updateBlockOnBookForm(({ upsert, deleteIds }) => ({
        upsert: upsert
          .filter((block) => block.id !== blockId)
          .map((block, index) => ({ ...block, sort: index + 1 })),
        deleteIds: blockId.startsWith('+')
          ? deleteIds
          : [...deleteIds, blockId],
      }));
    },
    [updateBlockOnBookForm],
  );

  const moveUpBlock = useCallback(
    (blockId: string) => {
      updateBlockOnBookForm(({ upsert, deleteIds }) => {
        const upBlock = upsert.find((block) => block.id === blockId);
        if (!upBlock) {
          throw new Error('Block not found.');
        }
        const downBlock = upsert.find(
          (block) => block.sort === upBlock.sort - 1,
        );
        if (!downBlock) {
          throw new Error('The sorting order is invalid.');
        }
        const newBlocks = {
          upsert: upsert
            .map((block) => {
              if (block.id === upBlock.id) {
                return { ...block, sort: block.sort - 1 };
              }
              if (block.id === downBlock.id) {
                return { ...block, sort: block.sort + 1 };
              }
              return block;
            })
            .sort((a, b) => a.sort - b.sort),
          deleteIds,
        };
        return newBlocks;
      });
    },
    [updateBlockOnBookForm],
  );

  const moveDownBlock = useCallback(
    (blockId: string) => {
      updateBlockOnBookForm(({ upsert, deleteIds }) => {
        const downBlock = upsert.find((block) => block.id === blockId);
        if (!downBlock) {
          throw new Error('Block is not found');
        }
        const upBlock = upsert.find(
          (block) => block.sort === downBlock.sort + 1,
        );
        if (!upBlock) {
          throw new Error('The sorting order is invalid');
        }
        const newBlocks = {
          upsert: upsert
            .map((block) => {
              if (block.id === upBlock.id) {
                return { ...block, sort: block.sort - 1 };
              }
              if (block.id === downBlock.id) {
                return { ...block, sort: block.sort + 1 };
              }
              return block;
            })
            .sort((a, b) => a.sort - b.sort),
          deleteIds,
        };
        return newBlocks;
      });
    },
    [updateBlockOnBookForm],
  );

  const toggleDisplayed = useCallback(
    (blockId: string) => {
      updateBlockOnBookForm(({ upsert, deleteIds }) => ({
        upsert: upsert.map((block) => {
          if (block.id === blockId) {
            return { ...block, displayed: !block.displayed };
          }
          return block;
        }),
        deleteIds,
      }));
    },
    [updateBlockOnBookForm],
  );

  return {
    addCustomBlock,
    updateBlock,
    deleteBlock,
    moveUpBlock,
    moveDownBlock,
    toggleDisplayed,
  };
}
