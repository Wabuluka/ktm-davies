import { NewsCategory } from '@/Features/NewsCategory/Types';
import { Link } from '@/UI/Components/Navigation/Link';
import { LinkBox, Tr, Td } from '@chakra-ui/react';

type Props = {
  category: NewsCategory;
};

export function NewsCategoryListItem({ category }: Props) {
  return (
    <LinkBox as={Tr} _hover={{ bg: 'gray.100' }}>
      <Td w={1}>
        <Link
          overlay
          href={route('news-categories.edit', category)}
          aria-label={`Edit ${category.name}`}
        >
          {category.id}
        </Link>
      </Td>
      <Td w={0} fontWeight="bold">
        {category.name}
      </Td>
    </LinkBox>
  );
}
