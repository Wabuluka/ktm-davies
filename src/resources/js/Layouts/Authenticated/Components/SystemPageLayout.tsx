import { SystemPageList } from '@/Features/Misc/PageList';

import { FC } from 'react';
import { AuthenticatedLayout } from '..';
import { LayoutProps } from '../Types';

type Props = Omit<LayoutProps, 'navigation' | 'pageCategory'>;

export const AuthenticatedSystemPageLayout: FC<Props> = ({
  title,
  children,
}) => {
  return (
    <AuthenticatedLayout
      title={title}
      pageCategory="System"
      navigation={<SystemPageList />}
    >
      {children}
    </AuthenticatedLayout>
  );
};
