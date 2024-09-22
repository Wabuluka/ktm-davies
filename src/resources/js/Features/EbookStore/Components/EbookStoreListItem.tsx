import { EbookStore } from '@/Features/EbookStore/Types';
import { Link } from '@/UI/Components/Navigation/Link';
import { ExternalLinkIcon } from '@chakra-ui/icons';
import { Badge, Td, Tr } from '@chakra-ui/react';

type EbookStoreListItemProps = {
  store: EbookStore;
};

export function EbookStoreListItem({
  store: ebookStore,
}: EbookStoreListItemProps) {
  return (
    <Tr>
      <Td>{ebookStore.id}</Td>
      <Td fontWeight="bold">{ebookStore.store.name}</Td>
      <Td>
        <Link
          href={ebookStore.store.url}
          target="_blank"
          rel="noopener noreferrer"
          isExternal
        >
          {ebookStore.store.url} <ExternalLinkIcon mx={1} />
        </Link>
      </Td>
      <Td fontWeight="bold" textColor="gray.600">
        {ebookStore.is_purchase_url_required ? (
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
