import { EbookStore } from '@/Features/EbookStore';
import { useEbookStore } from '@/Features/EbookStore/Hooks/useEbookStore';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { DeleteIcon } from '@chakra-ui/icons';
import { Center, HStack, IconButton, Radio, Td, Tr } from '@chakra-ui/react';
import { EbookStoreOnBookForm } from '../Types';

type Props = {
  ebookstore: EbookStoreOnBookForm;
  selectType?: 'radio' | 'none';
  onEdit: (ebookstore: EbookStoreOnBookForm) => void;
  onDelete: (ebookstore: EbookStoreOnBookForm) => void;
};

export function EbookStoreListItem({
  ebookstore,
  selectType = 'none',
  onEdit,
  onDelete,
}: Props) {
  const storeName = (useEbookStore(ebookstore.id) as EbookStore).store.name;

  function handleEditButtonClick() {
    onEdit(ebookstore);
  }
  function handleDeleteButtonClick() {
    onDelete(ebookstore);
  }

  return (
    <Tr>
      {selectType !== 'none' && (
        <Td p={0}>
          <Center>
            <Radio
              value={ebookstore.id}
              isChecked={ebookstore.is_primary}
              size="lg"
            />
          </Center>
        </Td>
      )}
      <Td>{storeName}</Td>
      <Td>{ebookstore.url}</Td>
      <Td>
        <HStack>
          <EditButton
            aria-label={`Edit ${storeName}`}
            onClick={handleEditButtonClick}
          />
          <IconButton
            as={DeleteIcon}
            aria-label={`Delete ${storeName}`}
            onClick={handleDeleteButtonClick}
            bg="red.500"
            color="white"
            p={2}
          />
        </HStack>
      </Td>
    </Tr>
  );
}
