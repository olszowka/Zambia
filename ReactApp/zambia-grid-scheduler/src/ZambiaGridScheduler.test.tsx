import React from 'react';
import { render, screen } from '@testing-library/react';
import ZambiaGridScheduler from './ZambiaGridScheduler';

test('renders learn react link', () => {
  render(<ZambiaGridScheduler />);
  const linkElement = screen.getByText(/learn react/i);
  expect(linkElement).toBeInTheDocument();
});
