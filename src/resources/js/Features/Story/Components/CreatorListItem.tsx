import { useShowCreatorQuery } from '@/Features/Creator';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { DeleteIcon } from '@chakra-ui/icons';
import {
  HStack,
  IconButton,
  ListItem,
  ListItemProps,
  Text,
} from '@chakra-ui/react';

export type CreatorListItemProps = {
  creatorId: string;
  onRemove: (creatorId: string) => void;
  onOrderUp?: (creatorId: string) => void;
  onOrderDown?: (creatorId: string) => void;
} & Omit<ListItemProps, 'children'>;

export function CreatorListItem({
  creatorId,
  onRemove,
  onOrderUp,
  onOrderDown,
  ...props
}: CreatorListItemProps) {
  const { data: creator, isLoading, isError } = useShowCreatorQuery(creatorId);

  function handleRemove() {
    onRemove(creatorId);
  }
  function handleOrderDown() {
    onOrderDown?.(creatorId);
  }
  function handleOrderUp() {
    onOrderUp?.(creatorId);
  }

  return (
    <ListItem {...props}>
      {isLoading ? (
        <LoadingSpinner />
      ) : isError || !creator ? (
        <DataFetchError />
      ) : (
        <HStack spacing={8}>
          <Text flex="1">
            {creator.name} {!!creator.name_kana && <>({creator.name_kana})</>}
          </Text>
          <HStack>
            <SortButtons
              onUp={handleOrderUp}
              onDown={handleOrderDown}
              disableUp={!onOrderUp}
              disableDown={!onOrderDown}
            />
            <IconButton
              as={DeleteIcon}
              aria-label={`Delete ${creator.name} from creator information.`}
              onClick={handleRemove}
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
