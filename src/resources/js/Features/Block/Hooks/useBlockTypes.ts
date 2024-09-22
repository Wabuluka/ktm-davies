import { usePage } from '@inertiajs/react';
import { useCallback } from 'react';
import { BlockType } from '../Types';

type PageProps = {
  master: { blockTypes: BlockType[] };
};

export function useBlockTypes() {
  const blockTypes = usePage<PageProps>().props.master.blockTypes;

  const getBlockTypeName = useCallback(
    (id: string | number) => {
      const blockType = blockTypes.find((block) => block.id == id);

      if (!blockType) {
        throw new Error(`${id} is invalid BlockType id`);
      }

      return blockType.name;
    },
    [blockTypes],
  );

  const isCommonBlock = useCallback((id: string | number) => id == 1, []);
  const isBookStoreBlock = useCallback((id: string | number) => id == 2, []);
  const isEbookStoreBlock = useCallback((id: string | number) => id == 3, []);
  const isBenefitBlock = useCallback((id: string | number) => id == 4, []);
  const isSeriesBlock = useCallback((id: string | number) => id == 5, []);
  const isRelatedBlock = useCallback((id: string | number) => id == 6, []);
  const isStoryBlock = useCallback((id: string | number) => id == 7, []);
  const isCharacterBlock = useCallback((id: string | number) => id == 8, []);
  const isCustomBlock = useCallback((id: string | number) => id == 9, []);

  return {
    blockTypes,
    getBlockTypeName,
    isCommonBlock,
    isBookStoreBlock,
    isEbookStoreBlock,
    isBenefitBlock,
    isSeriesBlock,
    isRelatedBlock,
    isStoryBlock,
    isCharacterBlock,
    isCustomBlock,
  };
}
