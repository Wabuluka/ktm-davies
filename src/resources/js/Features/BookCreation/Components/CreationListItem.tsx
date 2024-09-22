import { useShowCreatorQuery } from '@/Features/Creator/Hooks/useShowCreatorQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { DeleteIcon } from '@chakra-ui/icons';
import {
  HStack,
  IconButton,
  ListItem,
  ListItemProps,
  Text,
} from '@chakra-ui/react';
import { useBookCreationDispatcher } from '../Hooks/useBookCreationDispatcher';
import { CreationOnBookForm } from '../Types';

type Props = {
  creation: CreationOnBookForm;
  onEdit: (creation: CreationOnBookForm) => void;
  firstItem?: boolean;
  lastItem?: boolean;
} & Omit<ListItemProps, 'children'>;

export function CreationListItem({
  creation,
  onEdit,
  firstItem = false,
  lastItem = false,
  ...props
}: Props) {
  const {
    data: creator,
    isLoading,
    isError,
  } = useShowCreatorQuery(creation.creator_id);
  const { deleteCreation, moveDownCreation, moveUpCreation } =
    useBookCreationDispatcher();

  function handleEditButtonClick(e: React.MouseEvent<HTMLButtonElement>) {
    e.preventDefault();
    onEdit(creation);
  }
  function handleDeleteButtonClick() {
    deleteCreation(creation.creator_id);
  }
  function handleOrderDownButtonClick() {
    moveDownCreation(creation);
  }
  function handleOrderUpButtonClick() {
    moveUpCreation(creation);
  }

  return (
    <ListItem {...props}>
      {isLoading ? (
        <LoadingSpinner />
      ) : isError || !creator ? (
        <DataFetchError />
      ) : (
        <HStack spacing={8}>
          <Text flex="1 0 30%">
            {creator.name} {!!creator.name_kana && <>({creator.name_kana})</>}
          </Text>
          <Text>{creation.displayed_type || creation.creation_type}</Text>
          <HStack>
            <SortButtons
              onUp={handleOrderUpButtonClick}
              onDown={handleOrderDownButtonClick}
              disableUp={firstItem}
              disableDown={lastItem}
            />
            <EditButton
              onClick={handleEditButtonClick}
              aria-label={`Edit a relationship between ${creator.name}`}
            />
            <IconButton
              as={DeleteIcon}
              aria-label={`Delete a relationship between ${creator.name}`}
              onClick={handleDeleteButtonClick}
              bg="red.500"
              color="white"
              p={2}
            />
          </HStack>
        </HStack>
      )}
    </ListItem>
  );
}
