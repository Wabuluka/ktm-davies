import { NewsStatusBadge } from '@/Features/News/Components/NewsStatusBadge';
import { News } from '@/Features/News/Types';
import { Link } from '@/UI/Components/Navigation/Link';
import { Badge, Center, LinkBox, Td, Tr } from '@chakra-ui/react';

type Props = {
  news: News;
};

export function NewsListItem({ news }: Props) {
  return (
    <LinkBox as={Tr} _hover={{ bg: 'gray.100' }}>
      <Td w={1}>
        <Link
          overlay
          href={route('news.edit', { news })}
          aria-label={`Edit ${news.title}`}
        >
          {news.id}
        </Link>
      </Td>
      <Td w={1}>
        <Center>
          <NewsStatusBadge status={news.status} />
        </Center>
      </Td>
      <Td w={1}>
        <Badge variant="outline" px={4} py={2}>
          {news.category.name}
        </Badge>
      </Td>
      <Td w={0} fontWeight="bold">
        {news.title}
      </Td>
      <Td w={0}>{news.published_at}</Td>
    </LinkBox>
  );
}
