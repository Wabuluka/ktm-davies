import { SitePageList } from '@/Features/Misc/PageList';

import { FC } from 'react';
import { AuthenticatedLayout } from '..';
import { LayoutProps } from '../Types';

type Props = Omit<LayoutProps, 'navigation' | 'pageCategory'>;

export const AuthenticatedSitePageLayout: FC<Props> = ({ title, children }) => {
  return (
    <AuthenticatedLayout
      title={title}
      pageCategory="Site"
      navigation={<SitePageList />}
    >
      {children}
    </AuthenticatedLayout>
  );
};
