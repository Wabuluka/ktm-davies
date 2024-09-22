import { Paginator } from '@/Api/Types';
import { News } from '@/Features/News';
import { NewsList } from '@/Features/News/Components/NewsList';
import { SearchNewsForm } from '@/Features/News/Components/SearchNewsForm';
import { useIndexNews } from '@/Features/News/Hooks/useIndexNews';
import { Site } from '@/Features/Site';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { PaginatorBase } from '@/UI/Components/Navigation/PaginatorBase';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, LinkBox, VStack } from '@chakra-ui/react';
import { BsNewspaper, BsPlus } from 'react-icons/bs';

type Props = {
  newsPaginator: Paginator<News>;
  site: Site;
};

export default function Index({ newsPaginator, site }: Props) {
  const { params, errors, onSearchFormChange, onSearchSubmit, onPageChange } =
    useIndexNews({ site });
  function handleSearchSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    onSearchSubmit();
  }

  return (
    <VStack spacing={8} align="stretch">
      <Heading as="h2" mb={8} icon={<BsNewspaper />}>
        News一覧
      </Heading>
      <SearchNewsForm
        params={params}
        errors={errors}
        onChange={onSearchFormChange}
        onSubmit={handleSearchSubmit}
      />
      <Box textAlign="right">
        <PrimaryButton as={LinkBox} leftIcon={<BsPlus />}>
          <Link href={route('sites.news.create', site)} overlay>
            New
          </Link>
        </PrimaryButton>
      </Box>
      <NewsList newsList={newsPaginator.data} />
      <PaginatorBase
        onPageChange={onPageChange}
        lastPage={newsPaginator.meta.last_page}
        currentIndex={newsPaginator.meta.current_page}
      />
    </VStack>
  );
}

Index.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="News">{page}</AuthenticatedSitePageLayout>
);
