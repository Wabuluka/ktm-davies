import { Banner, BannerEditForm } from '@/Features/Banner';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';

type Props = {
  banner: Banner;
};

export default function Edit({ banner }: Props) {
  return <BannerEditForm banner={banner} />;
}

Edit.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title={`バナーを編集`}>
    {page}
  </AuthenticatedSitePageLayout>
);
