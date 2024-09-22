import Selection from '@/Features/Book/Components/Form/Selection';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { HStack, Image, Text } from '@chakra-ui/react';
import { useShowBookQuery } from '../Hooks/useShowBookQuery';

type Props = {
  bookId: number;
  onUnselect: () => void;
};

export function BookSelection({ bookId, onUnselect }: Props) {
  const { data: book, isLoading, isError } = useShowBookQuery(bookId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !book) {
    return <DataFetchError />;
  }

  return (
    <Selection onUnselect={onUnselect}>
      <HStack>
        {book.cover?.original_url && (
          <Image
            src={book.cover.original_url}
            alt={book.title}
            boxSize={16}
            rounded="full"
          />
        )}
        <Text>{book.title}</Text>
      </HStack>
    </Selection>
  );
}
