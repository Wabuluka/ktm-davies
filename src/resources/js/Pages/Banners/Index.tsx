import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, Flex, LinkBox, Spacer, VStack } from '@chakra-ui/react';
import { Banner, BannerPlacement } from '@/Features/Banner/Types';
import { BannerList } from '@/Features/Banner';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { BsPlus } from 'react-icons/bs';

type Props = {
  banners: Banner[];
  creatable: boolean;
  placement: BannerPlacement;
};

export default function Index({ banners, creatable, placement }: Props) {
  const createRoute = route('banner-placements.banners.create', {
    banner_placement: placement.id,
  });

  return (
    <VStack spacing={12} align="stretch">
      <Box as="section" w="100%">
        <Heading as="h2" mb={8}>
          バナー一覧 ({placement.name})
        </Heading>
        <Flex w="100%" mb={8}>
          <Spacer />
          {creatable && (
            <PrimaryButton as={LinkBox} leftIcon={<BsPlus />}>
              <Link href={createRoute} overlay>
                New
              </Link>
            </PrimaryButton>
          )}
        </Flex>
        <BannerList banners={banners} />
      </Box>
    </VStack>
  );
}

Index.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="バナー設定">
    {page}
  </AuthenticatedSitePageLayout>
);
