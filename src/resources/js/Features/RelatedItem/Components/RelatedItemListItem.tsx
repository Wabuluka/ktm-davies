import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { HStack, Td, Tr } from '@chakra-ui/react';
import { useShowRelatable } from '../Hooks/useShowRelatable';
import { RelatedItemOnBookForm } from '../Types';
import { RelatedItemType } from './RelatedItemType';

type Props = {
  relatedItem: RelatedItemOnBookForm;
  firstItem?: boolean;
  lastItem?: boolean;
  onOrderUp: (relatedItem: RelatedItemOnBookForm) => void;
  onOrderDown: (relatedItem: RelatedItemOnBookForm) => void;
  onEdit: (relatedItem: RelatedItemOnBookForm) => void;
  onDelete: (relatedItem: RelatedItemOnBookForm) => void;
};

export function RelatedItemListItem({
  relatedItem,
  firstItem = false,
  lastItem = false,
  onOrderUp,
  onOrderDown,
  onEdit,
  onDelete,
}: Props) {
  const {
    data: relatable,
    isLoading,
    isError,
    type,
  } = useShowRelatable({
    type: relatedItem.relatable_type,
    id: Number(relatedItem.relatable_id),
  });
  function handleEditButtonClick(e: React.MouseEvent<HTMLButtonElement>) {
    e.preventDefault();
    onEdit(relatedItem);
  }
  function handleOrderDown() {
    onOrderDown(relatedItem);
  }
  function handleOrderUp() {
    onOrderUp(relatedItem);
  }
  function handleDeleteButtonClick(e: React.MouseEvent<HTMLButtonElement>) {
    e.preventDefault();
    onDelete(relatedItem);
  }
  if (isLoading) {
    return (
      <Tr>
        <Td>
          <LoadingSpinner />
        </Td>
      </Tr>
    );
  }
  if (isError || !relatable) {
    return (
      <Tr>
        <Td>
          <DataFetchError />
        </Td>
      </Tr>
    );
  }
  const thumbnail = type === 'book' ? relatable.cover : relatable.thumbnail;

  return (
    <Tr>
      <Td p={0}>
        <RelatedItemType
          type={relatedItem.relatable_type}
          fontSize="lg"
          w="100%"
        />
      </Td>
      <Td>
        {relatedItem.description} ({relatable.title})
      </Td>
      <Td p={0}>
        {thumbnail?.original_url && (
          <PreviewableThumbnail
            previewTriggerProps={{
              w: '100%',
              p: 1,
              'aria-label': 'Preview thumbnail image',
            }}
            imageProps={{
              src: thumbnail.original_url,
              alt: '',
            }}
          />
        )}
      </Td>
      <Td>
        <HStack spacing={4}>
          <HStack>
            <SortButtons
              onUp={handleOrderUp}
              onDown={handleOrderDown}
              disableUp={firstItem}
              disableDown={lastItem}
            />
          </HStack>
          <EditButton
            onClick={handleEditButtonClick}
            aria-label={`Edit ${relatedItem.description}`}
          />
          <DangerButton onClick={handleDeleteButtonClick}>Delete</DangerButton>
        </HStack>
      </Td>
    </Tr>
  );
}
