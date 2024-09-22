import { BookStore } from '@/Features/BookStore/Types';
import { Link } from '@/UI/Components/Navigation/Link';
import { ExternalLinkIcon } from '@chakra-ui/icons';
import { Badge, Td, Tr } from '@chakra-ui/react';

type BookStoreListItemProps = {
  store: BookStore;
};

export function BookStoreListItem({
  store: bookStore,
}: BookStoreListItemProps) {
  return (
    <Tr>
      <Td>{bookStore.id}</Td>
      <Td fontWeight="bold">{bookStore.store.name}</Td>
      <Td>
        <Link
          href={bookStore.store.url}
          target="_blank"
          rel="noopener noreferrer"
          isExternal
        >
          {bookStore.store.url} <ExternalLinkIcon mx={1} />
        </Link>
      </Td>
      <Td fontWeight="bold" textColor="gray.600">
        {bookStore.is_purchase_url_required ? (
          <Badge colorScheme="red" size="sm" px={2} py={1}>
            Required
          </Badge>
        ) : (
          ''
        )}
      </Td>
    </Tr>
  );
}
