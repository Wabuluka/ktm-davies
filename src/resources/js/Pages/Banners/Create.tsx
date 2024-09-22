import { BannerCreateForm, BannerPlacement } from '@/Features/Banner';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';

type Props = {
  placement: BannerPlacement;
};

export default function Create({ placement }: Props) {
  return <BannerCreateForm placementId={placement.id} />;
}

Create.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title={`バナーを作成`}>
    {page}
  </AuthenticatedSitePageLayout>
);
