import { Box, Table, Tbody, Th, Thead, Tr } from '@chakra-ui/react';
import { Banner } from '@/Features/Banner/Types';
import { BannerListItem } from '@/Features/Banner/Components/BannerListItem';

type Props = {
  banners: Banner[];
};

export function BannerList({ banners }: Props) {
  return (
    <Box w="100%" overflowX="auto" overflowY="hidden">
      <Table size="md">
        <Thead>
          <Tr>
            <Th whiteSpace="nowrap">Name</Th>
            <Th whiteSpace="nowrap">URL</Th>
            <Th whiteSpace="nowrap" textAlign={'center'}>
              Open in new tab
            </Th>
            <Th whiteSpace="nowrap" textAlign={'center'}>
              Displayed
            </Th>
            <Th whiteSpace="nowrap" textAlign={'center'}>
              Image
            </Th>
            <Th whiteSpace="nowrap" textAlign={'center'}>
              Operation
            </Th>
          </Tr>
        </Thead>
        <Tbody>
          {banners.map((banner, i) => (
            <BannerListItem
              key={banner.id}
              banner={banner}
              isFirst={i === 0}
              isLast={i === banners.length - 1}
            />
          ))}
        </Tbody>
      </Table>
    </Box>
  );
}
