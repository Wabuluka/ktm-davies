import { useBlockDispatcher } from '@/Features/Book/Hooks/useBlockDispatcher';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { DeleteIcon } from '@chakra-ui/icons';
import { Center, Checkbox, HStack, IconButton, Td } from '@chakra-ui/react';
import { useBlockTypes } from '../Hooks/useBlockTypes';
import { BlockOnBookForm } from '../Types';

type Props = {
  block: BlockOnBookForm;
  isFirst?: boolean;
  isLast?: boolean;
  onEdit: (block: BlockOnBookForm) => void;
};

export function BlockListItem({
  block,
  isFirst = false,
  isLast = false,
  onEdit,
}: Props) {
  const {
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
  } = useBlockTypes();
  const { deleteBlock, moveUpBlock, moveDownBlock, toggleDisplayed } =
    useBlockDispatcher();
  const title = isCustomBlock(block.type_id)
    ? block.custom_title
    : getBlockTypeName(block.type_id);
  let editLabel: string | null;
  if (isCommonBlock(block.type_id)) {
    editLabel = null;
  } else if (isBookStoreBlock(block.type_id)) {
    editLabel = '購入先情報を編集';
  } else if (isEbookStoreBlock(block.type_id)) {
    editLabel = '購入先情報(電子)を編集';
  } else if (isBenefitBlock(block.type_id)) {
    editLabel = '店舗特典情報を編集';
  } else if (isSeriesBlock(block.type_id)) {
    editLabel = null;
  } else if (isRelatedBlock(block.type_id)) {
    editLabel = '関連作品情報を編集';
  } else if (isStoryBlock(block.type_id)) {
    editLabel = '収録作品情報を編集';
  } else if (isCharacterBlock(block.type_id)) {
    editLabel = 'キャラクター紹介を編集';
  } else if (isCustomBlock(block.type_id)) {
    editLabel = '自由欄を編集';
  } else {
    throw new Error('ブロックの種別が不正です');
  }

  function handleDisplayedChange() {
    toggleDisplayed(block.id);
  }
  function handleOrderUpButtonClick() {
    moveUpBlock(block.id);
  }
  function handleOrderDownButtonClick() {
    moveDownBlock(block.id);
  }
  function handleEditButtonClick() {
    onEdit(block);
  }
  function handleDeleteButtonClick() {
    deleteBlock(block.id);
  }

  return (
    <>
      <Td p={0}>
        <Center>
          <Checkbox
            isChecked={block.displayed}
            onChange={handleDisplayedChange}
            size="lg"
            py={4}
            px={6}
          />
        </Center>
      </Td>
      <Td>{title}</Td>
      <Td>
        <HStack spacing={8}>
          <SortButtons
            disableUp={isFirst}
            disableDown={isLast}
            onDown={handleOrderDownButtonClick}
            onUp={handleOrderUpButtonClick}
          />
          {!!editLabel && (
            <EditButton
              onClick={handleEditButtonClick}
              aria-label={editLabel}
            />
          )}
          {isCustomBlock(block.type_id) && (
            <IconButton
              as={DeleteIcon}
              aria-label={`${title}を削除する`}
              onClick={handleDeleteButtonClick}
              bg="red.500"
              color="white"
              p={2}
            />
          )}
        </HStack>
      </Td>
    </>
  );
}
