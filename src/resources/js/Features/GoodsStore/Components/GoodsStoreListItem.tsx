import { GoodsStore } from '@/Features/GoodsStore/Types';
import { Link } from '@/UI/Components/Navigation/Link';
import { ExternalLinkIcon } from '@chakra-ui/icons';
import { Td, Tr } from '@chakra-ui/react';

type GoodsStoreListItemProps = {
  store: GoodsStore;
};

export function GoodsStoreListItem({
  store: goodsStore,
}: GoodsStoreListItemProps) {
  return (
    <Tr>
      <Td>{goodsStore.id}</Td>
      <Td fontWeight="bold">{goodsStore.store.name}</Td>
      <Td>
        <Link
          href={goodsStore.store.url}
          target="_blank"
          rel="noopener noreferrer"
          isExternal
        >
          {goodsStore.store.url} <ExternalLinkIcon mx={1} />
        </Link>
      </Td>
    </Tr>
  );
}
