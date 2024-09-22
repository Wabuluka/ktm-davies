import { ReactNode } from 'react';
import { PageCategory } from '..';

export type LayoutProps = {
  title?: string;
  pageCategory?: PageCategory;
  navigation?: ReactNode;
  children: ReactNode;
};
