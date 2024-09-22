import { NewsCategoryList } from '@/Features/NewsCategory/Components/NewsCategoryList';
import { Site } from '@/Features/Site';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, LinkBox, VStack } from '@chakra-ui/react';
import { BsNewspaper, BsPlus } from 'react-icons/bs';

type Props = {
  site: Site;
};

export default function Index({ site }: Props) {
  return (
    <VStack spacing={8} align="stretch">
      <Heading as="h2" mb={8} icon={<BsNewspaper />}>
        News Category List
      </Heading>
      <Box textAlign="right">
        <PrimaryButton as={LinkBox} leftIcon={<BsPlus />}>
          <Link href={route('sites.news-categories.create', site)} overlay>
            New
          </Link>
        </PrimaryButton>
      </Box>
      <NewsCategoryList categories={site.news_categories} />
    </VStack>
  );
}

Index.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="News Category">
    {page}
  </AuthenticatedSitePageLayout>
);
