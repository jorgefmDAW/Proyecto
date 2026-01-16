import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ChatLiga } from './chat-liga';

describe('ChatLiga', () => {
  let component: ChatLiga;
  let fixture: ComponentFixture<ChatLiga>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ChatLiga]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ChatLiga);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
